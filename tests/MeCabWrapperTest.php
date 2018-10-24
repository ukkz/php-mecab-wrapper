<?php

namespace MeCabWrapperTest;

use PHPUnit\Framework\TestCase;
use MeCab\Sentence as MeCabSentence;
use MeCab\Word as MeCabWord;

class MeCabWrapperTest extends TestCase
{
    private $sample_text_1 = "[明石焼きについて]\n明石では、卵焼きと呼ばれている。しかし普通の卵焼きも卵焼きである。\n地元民としても非常にわかりづらい。";
    private $sample_text_2 = "港では「いかなご」が水揚げされていた. \n3月の末ごろに街を歩けば, 釘煮の, 甘辛い醤油の香りがあちこちにたちこめている. 今年も, 春がやってきたのだ.";

    /**
     * 文章を文ごとに分割する
     *
     * @return void
     */
    public function test_static_explode()
    {
        $expected_sentences_1 = [
            "[明石焼きについて]",
            "明石では、卵焼きと呼ばれている。",
            "しかし普通の卵焼きも卵焼きである。",
            "地元民としても非常にわかりづらい。",
        ];
        $expected_sentences_2 = [
            "港では「いかなご」が水揚げされていた.",
            "3月の末ごろに街を歩けば, 釘煮の, 甘辛い醤油の香りがあちこちにたちこめている.",
            "今年も, 春がやってきたのだ.",
        ];
        
        $this->assertEquals($expected_sentences_1, MeCabSentence::explode($this->sample_text_1));
        $this->assertEquals($expected_sentences_2, MeCabSentence::explode($this->sample_text_2));
    }

    /**
     * 分かち書き取得確認
     *
     * @return void
     */
    public function test_wakachi()
    {
        $expected_words_1 = ["明石","で","は","、","卵焼き","と","呼ば","れ","て","いる","。"];
        $expected_words_2 = ["港","で","は","「","いかなご","」","が","水揚げ","さ","れ","て","い","た","."];
        
        // "明石では、卵焼きと呼ばれている。"
        $tamago = new MeCabSentence( MeCabSentence::explode($this->sample_text_1)[1] );
        // "港では「いかなご」が水揚げされていた."
        $ikanago = new MeCabSentence( MeCabSentence::explode($this->sample_text_2)[0] );

        // 配列で全部取得するパターン
        $this->assertEquals($expected_words_1, $tamago->getAllSurfaces());
        $this->assertEquals($expected_words_2, $ikanago->getAllSurfaces());

        // ジェネレータを配列に
        $this->assertEquals($expected_words_1, iterator_to_array($tamago->getSurface()));
        $this->assertEquals($expected_words_2, iterator_to_array($ikanago->getSurface()));
    }

    /**
     * 文章をすべてカタカナの読み仮名にする
     *
     * @return void
     */
    public function test_convert_to_kana()
    {
        $expected_kana_1 = "ジモトミントシテモヒジョウニワカリヅライ。";
        $expected_kana_2 = "コトシモハルガヤッテキタノダ";
        
        // "地元民としても非常にわかりづらい。"
        $tamago = new MeCabSentence( MeCabSentence::explode($this->sample_text_1)[3] );
        // "今年も, 春がやってきたのだ."
        $ikanago = new MeCabSentence( MeCabSentence::explode($this->sample_text_2)[2] );

        $this->assertEquals($expected_kana_1, $tamago->toKana());
        $this->assertEquals($expected_kana_2, $ikanago->toKana());
    }

    /**
     * 文章のカタカナ発音を確認する
     *
     * @return void
     */
    public function test_check_pronunciation()
    {
        $expected_pron_1 = "ジモトミントシテモヒジョーニワカリズライ。"; // 「非常に」→「ヒジョーニ」・「わかりづらい」→「ワカリズライ」
        $expected_pron_2 = "ミナトデワ「イカナゴ」ガミズアゲサレテイタ"; // 「港では」→「ミナトデワ」
        
        // "地元民としても非常にわかりづらい。"
        $tamago = new MeCabSentence( MeCabSentence::explode($this->sample_text_1)[3] );
        // "港では「いかなご」が水揚げされていた."
        $ikanago = new MeCabSentence( MeCabSentence::explode($this->sample_text_2)[0] );

        $this->assertEquals($expected_pron_1, $tamago->toPronunciation());
        $this->assertEquals($expected_pron_2, $ikanago->toPronunciation());
    }

    /**
     * 単語ごとにメソッドが正しい値を出力しているか
     * 標準ipadicを使用した結果に基づく
     * （ipadicではコンマとドットは記号ではなく名詞扱いになる）
     *
     * @return void
     */
    public function test_word_methods()
    {
        // "今年も, 春がやってきたのだ."
        $sample_sentence = new MeCabSentence( MeCabSentence::explode($this->sample_text_2)[2] );
        $expected_class_array = ['名詞', '助詞', '名詞', '名詞', '助詞', '動詞', '助動詞', '名詞', '助動詞', '名詞'];
        $expected_classes_array = [
            ['名詞', '副詞可能'],
            ['助詞', '係助詞'],
            ['名詞', 'サ変接続'],
            ['名詞', '一般'],
            ['助詞', '格助詞', '一般'],
            ['動詞', '自立'],
            ['助動詞'],
            ['名詞', '非自立', '一般'],
            ['助動詞'],
            ['名詞', 'サ変接続']
        ];
        $expected_type_array = ['', '', '', '', '', 'カ変・クル', '特殊・タ', '', '特殊・ダ', ''];
        $expected_form_array = ['', '', '', '', '', '連用形', '基本形', '', '基本形', ''];
        $expected_base_array = ['今年', 'も', '', '春', 'が', 'やってくる', 'た', 'の', 'だ', ''];
        $expected_kana_array = ['コトシ', 'モ', '', 'ハル', 'ガ', 'ヤッテキ', 'タ', 'ノ', 'ダ', ''];
        $expected_pronunciation_array = ['コトシ', 'モ', '', 'ハル', 'ガ', 'ヤッテキ', 'タ', 'ノ', 'ダ', ''];

        // 形態素ごとに各メソッドを確認
        $word_class = $sample_sentence->getWord();
        for ($i=0; $i < 10; $i++) {
            $word = $word_class->current();
            $this->assertEquals($expected_class_array[$i], $word->class());
            $this->assertEquals($expected_classes_array[$i], $word->classes());
            $this->assertEquals($expected_type_array[$i], $word->type());
            $this->assertEquals($expected_form_array[$i], $word->form());
            $this->assertEquals($expected_base_array[$i], $word->base());
            $this->assertEquals($expected_kana_array[$i], $word->kana());
            $this->assertEquals($expected_pronunciation_array[$i], $word->pronunciation());
            $word_class->next();
        }
    } 
}
