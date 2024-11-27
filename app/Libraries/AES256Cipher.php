<?php

namespace Libraries;


use Exception;

class AES256Cipher
{
    private string $cipherAlgo = 'aes-256-cbc';
    private string $iv = 'abcH8BgTi!kEmnop';

    /**
     * 암호화
     *
     * @param string $type key 선택을 위한 구분자
     * @param string $data 암호화 할 평문
     * @param string|null $tableName 테이블 명
     * @param int|null $custIdx 사용자 인덱스
     * @return bool|string 실패 시, false
     * @throws Exception
     */
    public function encode(string $type, string $data, ?string $tableName = null, ?int $custIdx = null)
    {
        $key = $this->getPassphrase($type, $tableName, $custIdx);

        if ($key === false) {
            return false;
        }

        $cipherText = openssl_encrypt($data, $this->cipherAlgo, $key, true, $this->iv);

        if ($cipherText !== false) {
            return base64_encode($cipherText);
        } else {
            throw new Exception('암호화에 실패하였습니다.');
        }
    }

    /**
     * 암호화 키를 직접 넘겨 받아 암호화
     *
     * @param string $data 암호화 할 평문
     * @param string $key 암호화 키
     * @return string
     * @throws Exception
     */
    public function encodeWithKey(string $data, string $key): string
    {
        $cipherText = openssl_encrypt($data, $this->cipherAlgo, $key, true, $this->iv);

        if ($cipherText !== false) {
            return base64_encode($cipherText);
        } else {
            throw new Exception('암호화에 실패하였습니다.');
        }
    }

    /**
     * 복호화 키를 직접 넘겨 받아 복호화
     *
     * @param string $data 복호화 할 암호문
     * @param string $key 복호화 키
     * @return string
     * @throws Exception
     */
    public function decodeWithKey(string $data, string $key): string
    {
        $plainText = openssl_decrypt(base64_decode($data), $this->cipherAlgo, $key, true, $this->iv);

        if ($plainText !== false) {
            return $plainText;
        } else {
            throw new Exception('복호화에 실패하였습니다.');

        }
    }

    /**
     * 복호화
     *
     * @param string $type key 선택을 위한 구분자
     * @param string $data 복호화 할 암호문
     * @param string|null $tableName 테이블 명
     * @param int|null $custIdx 사용자 인덱스
     * @return bool|string 실패 시, false
     * @throws Exception
     */
    public function decode(string $type, string $data, ?string $tableName = null, ?int $custIdx = null)
    {
        $key = $this->getPassphrase($type, $tableName, $custIdx);

        if ($key === false) {
            return false;
        }

        $plainText = openssl_decrypt(base64_decode($data), $this->cipherAlgo, $key, true, $this->iv);

        if ($plainText !== false) {
            return $plainText;
        } else {
            throw new Exception('복호화에 실패하였습니다.');
        }
    }



}