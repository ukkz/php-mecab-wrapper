<?php
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
    # "「名」を聞いて「人」を知らぬと云う「こと」が随分ある。「人」ばかりではない。「すべて」の「物」にある。"

