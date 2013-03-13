<?php

final class CommonExceptionsTestCase extends ArcanistPhutilTestCase {
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
