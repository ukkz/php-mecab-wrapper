# PHP MeCab wrapper library

[MeCab](http://taku910.github.io/mecab/) wrapper library for PHP.

[![Build Status](https://travis-ci.com/ukkz/php-mecab-wrapper.svg?branch=master)](https://travis-ci.com/ukkz/php-mecab-wrapper)

## Install

`composer require ukkz/php-mecab-wrapper`

- 内部的には単純にmecabコマンドをexecしているだけなので、公式を参照してMeCabをインストールし、cliからMeCabが問題なく実行可能である環境を予め準備しておいてください。

```
$ echo "カツサンドはパンが両側からカロリーを押しつぶすから0カロリーである" | mecab
カツサンド      名詞,固有名詞,一般,*,*,*,カツサンド,カツサンド,カツサンド
は      助詞,係助詞,*,*,*,*,は,ハ,ワ
パン    名詞,一般,*,*,*,*,パン,パン,パン
が      助詞,格助詞,一般,*,*,*,が,ガ,ガ
両側    名詞,一般,*,*,*,*,両側,リョウガワ,リョーガワ
から    助詞,格助詞,一般,*,*,*,から,カラ,カラ
カロリー        名詞,一般,*,*,*,*,カロリー,カロリー,カロリー
を      助詞,格助詞,一般,*,*,*,を,ヲ,ヲ
押しつぶす      動詞,自立,*,*,五段・サ行,基本形,押しつぶす,オシツブス,オシツブス
から    助詞,接続助詞,*,*,*,*,から,カラ,カラ
0       名詞,数,*,*,*,*,*
カロリー        名詞,接尾,助数詞,*,*,*,カロリー,カロリー,カロリー
で      助動詞,*,*,*,特殊・ダ,連用形,だ,デ,デ
ある    助動詞,*,*,*,五段・ラ行アル,基本形,ある,アル,アル
EOS
```

- 辞書として[NEologd](https://github.com/neologd/mecab-ipadic-neologd)の使用を推奨します。（ただしテストは標準のipadic-0.996を使用）

## Usage

### classes

#### MeCab\Sentence

```
$mecab_sentence_class = new MeCab\Sentence("解析したい日本語文章", 辞書ディレクトリのパス);
$mecab_word_class_generator = $mecab_sentence_class->getWord();

foreach ($mecab_word_class_generator as $mecab_word_class) {
    // 形態素ごとの処理など
    echo $mecab_word_class . '/';
}
// 出力は "解析/し/たい/日本語/文章/" となります。
```

`getWord()`メソッドでMeCab\Wordクラスのジェネレータを返します。  
扱いにくい場合は`getAllWords()`でMeCab\Wordクラスの配列を得ることができます。  
constructの第2引数は使いたい辞書があれば指定してください（省略可能）。

#### MeCab\Word

`classes()`メソッド以外はすべて文字列:

```
$mecab_word_class->class();         // 品詞: 動詞・名詞など
$mecab_word_class->classes();       // 品詞の下位分類（配列）
$mecab_word_class->type();          // 活用型: サ行変格・ラ行五段など
$mecab_word_class->form();          // 活用形: 連用形・基本形など
$mecab_word_class->base();          // 原形
$mecab_word_class->kana();          // 読み仮名（カナ）
$mecab_word_class->pronunciation(); // 発音（カナ）
```

`class()`メソッドで品詞を返します。  
`classes()`メソッドで得られる配列は少なくとも1要素が含まれ、先頭の要素は`class()`の値と同じです。品詞の下位分類がある場合は最大4要素となります。  
記号など一部情報が存在しない（活用形がないなどの）メソッドは空文字列を返します。

wordクラスをechoした場合、表層形（もとの文章中で出現したままの形）の文字列が表示されます。


### sample.php

```
use MeCab\Sentence as MeCabSentence;

require_once('vendor/autoload.php');

// 入力文章
$original_text = '名を聞いて人を知らぬと云うことが随分ある。人ばかりではない。すべての物にある。';
$sample_sentence = new MeCabSentence($original_text);

// すべてカタカナにする
echo $sample_sentence->toKana() . "\n";
# "ナヲキイテヒトヲシラヌトイウコトガズイブンアル。ヒトバカリデハナイ。スベテノモノニアル。"

// 名詞だけ括弧でくくる
foreach ($sample_sentence->getWord() as $sample_word) {
    if ($sample_word->class() === '名詞') {
        echo '「' . $sample_word . '」';
    } else {
        echo $sample_word;
    }
}
# 「名」を聞いて「人」を知らぬと云う「こと」が随分ある。「人」ばかりではない。「すべて」の「物」にある。
```

## Requirements

- [MeCab](http://taku910.github.io/mecab/)
- PHP: >= 7.0

## Releases

|Date|Version|Description|
|:--|:--|:--|
|Oct 19, 18|1.1|辞書を指定できるようになった / add option for the dictionaries|
|Oct 25, 18|1.0|First release|

## ToDo

- 文字コードをはっきりさせたい

## License

MIT License:  
See LICENSE.txt .