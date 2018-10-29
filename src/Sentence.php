<?php

namespace MeCab;

class Sentence
{
    const SENTENCE_DELIMITER = [". ", "。"];
    const MECAB_LINE_END = 'EOS';
    private $command_raw_output = [];

    public function __construct(string $japanese_sentence, string $dictionary_directory = null)
    {
        // 辞書ディレクトリの指定があるか
        $dic_option = (empty($dictionary_directory)) ? '' : ' -d ' . escapeshellarg($dictionary_directory);
        // mecab実行
        $cmd = 'echo ' . escapeshellarg($japanese_sentence) . ' | mecab' . $dic_option;
        $last_row = exec($cmd, $this->command_raw_output, $exit_code);

        if ($exit_code === 127) {
            // コマンドが見つからないとき
            throw new \Exception('Command "mecab" is not found');
        } elseif (strpos($last_row, 'no such file or directory') !== false) {
            // 辞書ファイルが指定のディレクトリに見つからないとき
            throw new \Exception('Dictionary is not found in ' . $dictionary_directory);
        } elseif ($exit_code !== 0) {
            // それ以外のよくわからんエラー
            throw new \Exception('Unknown error occured: ' . $last_row);
        }
    }

    /**
     * Wordクラスのジェネレータを返す。
     *
     * @return iterable (\MeCab\Word)
     */
    public function getWord(): iterable
    {
        // 出力行ごと（形態素ごと）に処理
        foreach ($this->command_raw_output as $line) {
            if ($line === self::MECAB_LINE_END) {
                continue; // 文末または改行をスキップ
            }
            $word_line = explode("\t", $line, 2);
            $surface = $word_line[0];
            $detail = explode(',', $word_line[1], 9);

            yield new Word($surface, $detail);
        }
    }

    /**
     * 表層形（文字列）のジェネレータを返す。
     *
     * @return iterable (string)
     */
    public function getSurface(): iterable
    {
        // 出力行ごとに処理
        foreach ($this->command_raw_output as $line) {
            if ($line === self::MECAB_LINE_END) {
                continue; // 文末または改行をスキップ
            }
            $word_line = explode("\t", $line, 2);
            yield $word_line[0];
        }
    }

    /**
     * 単語ごとのWordクラスの配列を返す。
     *
     * @return array (\MeCab\Word)
     */
    public function getAllWords(): array
    {
        return iterator_to_array($this->getWord());
    }

    /**
     * 表層形（文字列）の配列を返す。
     * わかち書きを配列に格納したようなもの。
     *
     * @return array (string)
     */
    public function getAllSurfaces(): array
    {
        return array_map(function($w){return (string)$w;}, $this->getAllWords());
    }

    /**
     * 文章の読み仮名（カタカナ）を返す。
     *
     * @return string
     */
    public function toKana(): string
    {
        $kana_sentence = '';
        foreach ($this->getWord() as $word) {
            $kana_sentence .= $word->kana();
        }

        return $kana_sentence;
    }

    /**
     * 文章の発音（カタカナ）を返す。
     *
     * @return string
     */
    public function toPronunciation(): string
    {
        $pron_sentence = '';
        foreach ($this->getWord() as $word) {
            $pron_sentence .= $word->pronunciation();
        }

        return $pron_sentence;
    }

    /**
     * 日本語文章を改行や句点などの区切りをもとに文に分割して配列で返す。
     * （MeCab本体とは関係のない補助メソッド）
     *
     * @param string $longtext
     * @return array (string)
     */
    public static function explode(string $longtext): array
    {
        $alt_delimiter = array_map(function($d){return $d."\n";}, self::SENTENCE_DELIMITER); // 各デリミタにLF追加
        $alt_longtext = str_replace(self::SENTENCE_DELIMITER, $alt_delimiter, $longtext); // 置換
        $splitted_text = str_replace(["\r", "\r\n"], "\n", $alt_longtext); // 改行コードを統一
        $exploded_text = explode("\n", $splitted_text); // 分割
        // 空白でない文章のみ返す
        $result = [];
        foreach ($exploded_text as $sentence) {
            $t = trim($sentence);
            if (!empty($t)) {
                $result[] = $t;
            }
        }
        return $result;
    }
}
