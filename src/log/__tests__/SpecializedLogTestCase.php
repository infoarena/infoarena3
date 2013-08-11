<?php

final class SpecializedLogTestCase extends ArcanistPhutilTestCase {
    public function testDummyLog() {
        try {
            $log = new SpecializedLog('', '');
            $log->printData(array());
            $log->printMessage('');
            $log->printMessage("Hello");
        } catch (Exception $e) {
            $this->assertFailure($e->getMessage());
        }
    }

    public function testNoKeepData() {
        $log = null;
        try {
            $log = new SpecializedLog('', '%m');
            $log->printData(array('m' => 'Hello'));
            $log->printMessage('Second Hello');
        } catch (Exception $e) {
            $this->assertFailure($e->getMessage());
        }

        $this->assertEqual(
            '', $log->getStoredData(),
            'When keep_data is false no data should be saved');
    }

    public function testKeepData() {
        $log = null;
        try {
            $log = new SpecializedLog('', '%m', true);
            $log->printData(array('m' => 'Hello'));
            $log->printMessage('Second Hello');
        } catch (Exception $e) {
            $this->assertFailure($e->getMessage());
        }

        $this->assertEqual(
            "Hello\nSecond Hello\n",
            $log->getStoredData(),
            'When keep_data is true all the data must be kept');
    }
}
