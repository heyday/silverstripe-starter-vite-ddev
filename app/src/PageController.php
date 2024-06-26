<?php

namespace {

    use SilverStripe\CMS\Controllers\ContentController;
    use SilverStripe\Control\Director;
    use SilverStripe\View\Requirements;

    class PageController extends ContentController
    {
        private static $allowed_actions = [];

        /** @var boolean */
        protected $hot_vite_server_enabled = true;

        public function IsDevHot()
        {
            return Director::isDev() && $this->hot_vite_server_enabled;
        }

        public function getViteBaseHref(): string
        {
            if (Director::is_https()) {
                return rtrim(Director::absoluteBaseURL(), '/') . ':5174';
            } else {
                return rtrim(Director::absoluteBaseURL(), '/') . ':5173';
            }
        }


        public function getIncludeRequirements()
        {
            $manifestFile = Director::baseFolder() . '/app/client/dist/manifest.json';

            if (!file_exists($manifestFile)) {
                throw new Exception('client/dist/manifest.json does not exist. Please run `ddev yarn build` or `ddev yarn dev`');
            }

            $manifest = json_decode(file_get_contents($manifestFile), true);

            if (!$manifest) {
                throw new Exception('client/dist/manifest.json is not valid JSON. Please run `ddev yarn build` or `ddev yarn dev`');
            }

            Requirements::css('app/client/dist/' . $manifest['app/client/src/index.css']['file']);
            Requirements::javascript('app/client/dist/' . $manifest['app/client/src/index.js']['file']);

            if ($this->hasMethod('getAdditionalRequirements')) {
                $this->getAdditionalRequirements($manifest);
            }
        }


        public function getBannerProps(): string
        {
            return json_encode([
                'title' => (string) $this->Title,
            ]);
        }
    }
}
