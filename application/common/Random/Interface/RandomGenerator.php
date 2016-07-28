<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author oguray
 */
interface Common_Random_Interface_RandomGenerator
{

    /**
     * ランダムジェネレータの初期化
     * 
     * @param int $seed ランダム値の種
     */
    public function init($seed);

    /**
     * ランダム値を生成し、返す。
     * 
     * @return int ランダム値
     */
    public function nextInt();

    /**
     * floatなランダム値を返す。
     * 
     * 0.0 から 1.0 の間を返す。
     * 
     * @return float ランダム値
     */
    public function nextDouble();

    /**
     * 初期化時のSEEDを返す。
     * 
     * @return int SEED
     */
    public function getSeed();

    /**
     * ランダム値を生成し、返す。
     * 
     * @return int ランダム値
     */
    public function generate();
}
