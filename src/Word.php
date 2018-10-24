<?php

namespace MeCab;

class Word
{
    private $surface;
    private $classes;
    private $type;
    private $form;
    private $base;
    private $kana;
    private $pronunciation;

    /**
     * 直接newせずSentenceクラスのgetWord()などから利用のこと
     * ------------------------------------------------
     * 引数(1) string : 表層形
     * argment(1) : surface
     * 引数(2) array[9] : 品詞,品詞細分類1,品詞細分類2,品詞細分類3,活用型,活用形,原形,読み,発音
     * argument(2) : class(parts of speech),class1,class2,class3,type(conjugation-type),form(conjugation-form),base(infinitive),kana,pronunciation
     *
     * @param string $surface
     * @param array $detail
     */
    public function __construct(string $surface, array $detail)
    {
        $this->surface = $surface;

        // $detail配列の整形（必ず9要素にする）
        $this->classes[] = $detail[0]; // class(parts of speech)
        (!empty($detail[1]) && $detail[1] !== '*') && $this->classes[] = $detail[1]; // class1
        (!empty($detail[2]) && $detail[2] !== '*') && $this->classes[] = $detail[2]; // class2
        (!empty($detail[3]) && $detail[3] !== '*') && $this->classes[] = $detail[3]; // class3
        $this->type = (empty($detail[4]) || $detail[4] === '*') ? '' : $detail[4]; // type(conjugation-type)
        $this->form = (empty($detail[5]) || $detail[5] === '*') ? '' : $detail[5]; // form(conjugation-form)
        $this->base = (empty($detail[6]) || $detail[6] === '*') ? '' : $detail[6]; // base(infinitive)
        $this->kana = (empty($detail[7]) || $detail[7] === '*') ? '' : $detail[7]; // kana
        $this->pronunciation = (empty($detail[8]) || $detail[8] === '*') ? '' :$detail[8]; // pronunciation
    }

    /**
     * 品詞
     *
     * @return string
     */
    public function class(): string
    {
        return $this->classes[0];
    }

    /**
     * 品詞（細分類を含む配列）
     * 少なくとも1要素が含まれる
     * 最大4要素（品詞1+細分類3）
     *
     * @return array
     */
    public function classes(): array
    {
        return $this->classes;
    }

    /**
     * 活用型
     *
     * @return string
     */
    public function type(): string
    {
        return $this->type;
    }

    /**
     * 活用形
     *
     * @return string
     */
    public function form(): string
    {
        return $this->form;
    }

    /**
     * 原形
     *
     * @return string
     */
    public function base(): string
    {
        return $this->base;
    }

    /**
     * 読み（カタカナ）
     *
     * @return string
     */
    public function kana(): string
    {
        return $this->kana;
    }

    /**
     * 発音（カタカナ）
     *
     * @return string
     */
    public function pronunciation(): string
    {
        return $this->pronunciation;
    }

    /**
     * (string)表層形
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->surface;
    }
}
