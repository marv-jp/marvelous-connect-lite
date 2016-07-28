<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ParkMiller
 *
 * @author oguray
 */
class Common_Random_ParkMiller implements Common_Random_Interface_RandomGenerator
{
    /** @var int 種 */
    const A = 16807;

    /** @var int 種 */
    const M = 2147483647;

    private $_originalSeed;

    private $_seed;

    public function generate()
    {
        return $this->nextInt();
    }

    public function getSeed()
    {
        return $this->_originalSeed;
    }

    public function init($seed)
    {
        $this->_originalSeed = $seed;
        $this->_seed = $seed;
    }

    public function nextDouble()
    {
        return $this->generate() / self::M;
    }

    public function nextInt()
    {
        return $this->_seed = ($this->_seed * self::A) % self::M;
    }

}
