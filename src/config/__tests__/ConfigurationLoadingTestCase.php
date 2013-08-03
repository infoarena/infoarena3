<?php

final class ConfigurationLoadingTestCase extends ArcanistPhutilTestCase {
    public function testEmptyConfiguration() {
        try {
            new Configuration('{}', '{}');
        } catch (Exception $e) {
            $this->assertFailure($e->getMessage());
        }
    }

    public function testWhiteSpace() {
        try {
            new Configuration('{    }', '{}');
            new Configuration('{}', '{    }');
        } catch (Exception $e) {
            $this->assertFailure($e->getMessage());
        }
    }

    public function testOverwrittenValues() {
        $configuration = null;
        try {
            $configuration =
                new Configuration(
                    '{
                        "name":"Infoarena",
                        "friends": {
                            "boys":["InfoBoys"],
                            "girls":[]
                        }
                    }',
                    '{
                        "age":15,
                        "name": "Default Name",
                        "friends": {
                            "boys":[],
                            "girls":["InfoGirls"],
                            "computers":["Infoarena 2.0"]
                        }
                    }');
        } catch (Exception $e) {
            $this->assertFailure($e->getMessage());
        }

        $this->assertEqual('Infoarena',
                            $configuration->name,
                            'Default Value not overwritten');
        $this->assertEqual(15,
                           $configuration->age,
                           'Default Value not implied');
        $this->assertEqual(array("InfoBoys"),
                           $configuration->friends->boys,
                           'Default Value not overwritten');
        $this->assertEqual(array(),
                           $configuration->friends->girls,
                           'Default Value not overwritten with empty array');
        $this->assertEqual(array("Infoarena 2.0"),
                           $configuration->friends->computers,
                           'Default Value not implied');
    }

    public function testInvalidExtraKeys() {
        $configuration = null;
        try {
            $configuration =
                new Configuration(
                    '{"name":"Infoarena"}',
                    '{}');
            throw new Exception('No exception thrown');
        } catch (ConfigurationException $e) {
        } catch (Exception $e) {
            $this->assertFailure($e->getMessage());
        }

        try {
            $configuration =
                new Configuration(
                    '{
                        "friends": [
                            {
                                "name":"Info Boys",
                                "age: "26"
                            }
                    }',
                    '{
                        "friends": [
                            {
                                "name":"Infoarena 2.0"
                            }
                    }');
            throw new Exception('No exception thrown');
        } catch (ConfigurationException $e) {
        } catch (Exception $e) {
            $this->assertFailure($e->getMessage());
        }
    }

    public function testMismatchedTypes() {
        $configuration = null;
        try {
            $configuration =
                new Configuration('{"age":"15"}', '{"age":15}');
            throw new Exception(
                'No exception thrown when string is mismatched with integer');
        } catch (ConfigurationException $e) {
        } catch (Exception $e) {
            $this->assertFailure($e->getMessage());
        }

        try {
            $configuration =
                new Configuration('[]', '["ceva", 2]');
            throw new Exception('No exception thrown');
        } catch (ConfigurationException $e) {
        } catch (Exception $e) {
            $this->assertFailure($e ->getMessage());
        }
    }
}
