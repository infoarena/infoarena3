<?php

final class CommonExceptionsTestCase extends ArcanistPhutilTestCase {
    public function testSecurityLevelException() {
        try {
            throw new SecurityLevelException('SecurityLevel Exception Test');
        } catch (SecurityLevelException $e) {
            $this->assertEqual($e->getMessage(),
                               'SecurityLevel Exception Test',
                               'Different exception messages');
            $this->assertEqual($e->getUserMessage(),
                               'Internal Error',
                               'Different exception user messages');
        } catch (Exception $e) {
            $this->assertFailure('Wrong exception thrown');
        }
    }

    public function testLanguageException() {
        try {
            throw new LanguageException('Language Exception Test');
        } catch (LanguageException $e) {
            $this->assertEqual($e->getMessage(),
                               'Language Exception Test',
                               'Different exception messages');
            $this->assertEqual($e->getUserMessage(),
                               'Internal Language Error',
                               'Different exception user messages');
        } catch (Exception $e) {
            $this->assertFailure('Wrong exception thrown');
        }
    }
}
