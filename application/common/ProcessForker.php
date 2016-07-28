<?php

/**
 * プロセスフォーククラス。
 * run処理は全子プロセスの終了を待つ。
 * 
 * @see http://d.hatena.ne.jp/koyhoge/20111208/processforker
 */
class Common_ProcessForker
{
    // defaults
    protected $_options = array(
        'max_children'     => 10, // max number of child processes
        'process_limit'    => 0, // return when this count tasks are finished
        'loop_task'        => false, // reuse tasks
        'sleep'            => 0, // microseconds
        'single_execution' => null,
    );

    protected $_idx_task = 0;

    public function __construct($options = null)
    {
        if (!empty($options))
        {
            $this->_options = array_merge($this->_options, $options);
        }
    }

    public function fetchTask(&$tasks)
    {
        if ($this->_options['loop_task'])
        {
            $idx  = $this->_idx_task;
            $task = $tasks[$idx];

            // circulation in tasks
            ++$idx;
            if ($idx >= count($tasks))
            {
                $idx = 0;
            }
            $this->_idx_task = $idx;
        }
        else
        {
            $task = array_shift($tasks);
        }
        return $task;
    }

    public function run($tasks)
    {
        // number of current running child processes
        $nchildren = 0;
        // number of finished task
        $nfinished = 0;
        $pstack    = array();
        for (;;)
        {
            if (empty($tasks))
            {
                break;
            }
            $maxproc = $this->_options['process_limit'];
            if (($maxproc > 0) && ($maxproc <= $nfinished))
            {
                break;
            }
            if ($nchildren < $this->_options['max_children'])
            {
                $task = $this->fetchTask($tasks);

                $pid = pcntl_fork();
                if ($pid === -1)
                {
                    throw new Exception('pcntl_fork faild');
                }
                else if ($pid)
                {
                    // parent process
                    ++$nchildren;
                    $pstack[$pid] = true;
                }
                else
                {
                    $exit_code = 0;
                    // child process
                    $func      = $task[0];
                    $arg       = $task[1];
                    if (!is_array($arg))
                    {
                        $arg = array($arg);
                    }

                    try
                    {
                        call_user_func_array($func, $arg);
                    }
                    catch (Exception $e)
                    {
                        $exit_code = -1;
                    }
                    // care singleExecution:
                    // child process must not unlock
                    $se = $this->_options['single_execution'];
                    if ($se !== null)
                    {
                        $se->setDoUnlock(false);
                    }
                    exit($exit_code);
                }

                $sleep = $this->_options['sleep'];
                if ($sleep > 0)
                {
                    usleep($sleep);
                }
            }
            else
            {
                $pid = pcntl_waitpid(-1, $status, WUNTRACED);
                unset($pstack[$pid]);
                --$nchildren;
                ++$nfinished;
            }
        }
        //子プロセスの終了を待つ
        while (count($pstack) > 0)
        {
            unset($pstack[pcntl_waitpid(-1, $status, WUNTRACED)]);
        }
    }

}