<?php

final class WikiExceptionTestCase extends ArcanistPhutilTestCase {
    public function testWikiParameterException() {
        try {
            throw new WikiParameterException('WikiParameter Exception Test');
        } catch (WikiParameterException $e) {
            $this->assertEqual($e->getMessage(),
                               'WikiParameter Exception Test',
                               'Different exception messages');
            $this->assertEqual($e->getUserMessage(),
                               'Wiki Error',
                               'Different exception user messages');
        } catch (Exception $e) {
            $this->assertFailure('Wrong exception thrown');
        }

        try {
            throw new WikiParameterException() ;
        } catch (WikiException $e) {
            // Do nothing, it's ok
        } catch (Exception $e) {
            $this->assertFailure('WikiParameterException does not inherit' .
                                 'from WikiException');
        }
    }
}
