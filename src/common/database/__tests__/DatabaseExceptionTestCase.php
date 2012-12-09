<?php

final class DatabaseExceptionTestCase extends ArcanistPhutilTestCase {
    public function testDatabaseException() {
        try {
            throw new DatabaseException('Database Exception Test');
        } catch (DatabaseException $e) {
            $this->assertEqual($e->getMessage(), 'Database Exception Test',
                               'Different exception messages');
            $this->assertEqual($e->getUserMessage(), 'Database Error',
                               'Different exception user messages');
        } catch (Exception $e) {
            $this->assertFailure('Wrong exception thrown');
        }
    }
}
