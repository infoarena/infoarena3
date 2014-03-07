<?php

/**
 * We just add the style and the scripts
 */
abstract class ResourcedPageView extends BasicPageView {
    protected function getHead() {
        $style_location = '/assets/generated/'.
            InfoarenaEnvironment::getStaticFiles()->css;

        $style = phutil_tag(
            'link',
            array(
                'rel' => 'stylesheet',
                'href' => $style_location));
        return $style;
    }

    protected function getFooter() {
        $jquery = phutil_tag(
            'script',
            array(
                'src' => '//ajax.googleapis.com/ajax/libs/jquery/2.0.3/'.
                'jquery.min.js'));
        $script_location = "/assets/generated/" .
            InfoarenaEnvironment::getStaticFiles()->js;
        $script = phutil_tag(
            'script',
            array(
                'src' => $script_location));
        return hsprintf("%s %s", $jquery, $script);
    }
}
