<?php

/**
 * Class to load a configuration file based on a default configuration file
 * To be used for the Judge and Main Site
 */
final class Configuration {
    private $configuration;
    /**
     * Accepts two strings the user and the default configuration
     *
     * @param string $user_config_path
     * @param string $default_config_path
     * @return void
     */
   public function __construct($user_config, $default_config) {
        $user_config = StringUtils::removeComments($user_config);
        $default_config = StringUtils::removeComments($default_config);

        $user_config =
            json_decode($user_config);

        if ($user_config === null)
            throw new ConfigurationException(
                "JSON parsing error in user configuration file");

        $default_config =
            json_decode($default_config);

        if ($default_config === null)
            throw new ConfigurationException(
                "JSON parsing error in default configuration file");

        $this->checkConsistency($user_config, $default_config);

        $this->configuration =
            $this->buildConfiguration($user_config, $default_config);
   }

    /**
     * Extracts the information from the configuration
     *
     * @param string $key
     */
    public function __get($key) {
        if (!isset($this->configuration->$key)) {
            throw new ConfigurationException(
                "Key " . $key . " not found in this configurtion");
        }

        return $this->configuration->$key;
    }

   /**
    * Checks the user configuration matches that default configuration
    * That means no extra options, mismatched types or null elements in default
    * configuration file
    * Recursivly called for objects only
    *
    * @param string $user_config
    * @param string $default_config
    * @param bool $both_in_default_config
    * @return void
    */
    private function checkConsistency($user_config, $default_config,
                                      $both_in_default_config = false) {
        if ($default_config === null) {
            throw new ConfigurationException("
                Null value found in default configuration file");
        }

        if (gettype($user_config) != gettype($default_config)) {
            if ($both_in_default_config) {
                throw new ConfigurationException("
                    Mismatched types in default configuration file");
            } else {
                throw new ConfigurationException("
                    Mismatched types in user and default configuration files");
            }
        }

        // if they are both arrays we check the contain same type of elements
        // in our configuration files we won't have containers having variables
        // of different types
        if (is_array($user_config)) {
            for ($i = 1; $i < count($default_config); ++$i) {
                $this->checkConsistency($default_config[$i - 1],
                                        $default_config[$i], true);
            }

            if (count($user_config) && count($default_config)) {
                foreach ($user_config as $element) {
                    $this->checkConsistency($element, $default_config[0],
                                            $both_in_default_config);
                }
            }
        }

        if (is_object($user_config)) {
            foreach ($user_config as $key => $value) {
                if (!isset($default_config->$key)) {
                    throw new ConfigurationException("
                        Unrecognized option in user configuration file: " .
                        $key);
                }

                $this->checkConsistency($value, $default_config->$key,
                                        $both_in_default_config);
            }

            // we have to check the default config is correct also
            foreach ($default_config as $key => $value) {
                if (!isset($user_config->$key)) {
                    $this->checkConsistency($value, $value, true);
                }
            }
        }
    }

    /**
     * Builds the configuration file replacing default configurations with
     * user ones
     *
     * @param string $user_config
     * @param string $default_config
     * @return bool|int|float|string|array|object
     */
    private function buildConfiguration($user_config, $default_config) {
        if (!is_object($user_config) && !is_array($user_config)) {
            return $user_config;
        }

        // if it's an array and it contains objects or arrays(unlikely) we
        // recursively call this function
        if (is_array($user_config) && count($user_config) &&
            count($default_config)) {
            if (is_object($user_config[0]) || is_array($user_config[0])) {
                foreach ($user_config as &$element) {
                    $element = $this->buildConfiguration($element,
                                                         $default_config[0]);
                }
            }
        }

        if (is_object($user_config)) {
            foreach ($user_config as $key => &$value) {
                $value = $this->buildConfiguration($value,
                                                   $default_config->$key);
            }

            unset($value);

            foreach ($default_config as $key => $value) {
                if (!isset($user_config->$key)) {
                    $user_config->$key = $value;
                }
            }
        }

        return $user_config;
    }
}
