<?php
namespace CosmoCode\SimpleForm\Interceptor;

    /***************************************************************
     *  Copyright notice
     *
     *  (c) 2013 Markus Baumann <baumann@cosmocode.de>
     *
     *  All rights reserved
     *
     *  This script is part of the TYPO3 project. The TYPO3 project is
     *  free software; you can redistribute it and/or modify
     *  it under the terms of the GNU General Public License as published by
     *  the Free Software Foundation; either version 3 of the License, or
     *  (at your option) any later version.
     *
     *  The GNU General Public License can be found at
     *  http://www.gnu.org/copyleft/gpl.html.
     *
     *  This script is distributed in the hope that it will be useful,
     *  but WITHOUT ANY WARRANTY; without even the implied warranty of
     *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     *  GNU General Public License for more details.
     *
     *  This copyright notice MUST APPEAR in all copies of the script!
     ***************************************************************/

/**
 *
 *
 * @package simple_form
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
abstract class AbstractInterceptor {

    /**
     * @var \CosmoCode\SimpleForm\Utility\Validation\ValidationErrorHandler
     * @inject
     */
    protected $validationErrorHandler;

    /**
     * @var \CosmoCode\SimpleForm\Utility\Form\FormDataHandler
     * @inject
     */
    protected $formDataHandler;

    /**
     * @var array
     */
    protected $interceptorConfiguration;

    /**
     * @var \CosmoCode\SimpleForm\Utility\Session\SessionDataHandler
     * @inject
     */
    protected $sessionDataHandler;

    /**
     * @var \CosmoCode\SimpleForm\Utility\Form\StepHandler
     * @inject
     */
    protected $stepHandler;

    abstract public function intercept();

    /**
     * @param array $interceptorConfiguration
     */
    public function setInterceptorConfiguration($interceptorConfiguration) {
        if (isset($interceptorConfiguration['enableTypoScriptProcessing'])
            && $interceptorConfiguration['enableTypoScriptProcessing'] === '1'
        ) {
            \CosmoCode\SimpleForm\Utility\TypoScript::processTypoScriptSetupRecursive($interceptorConfiguration);
        }

        $this->interceptorConfiguration = $interceptorConfiguration;
    }

    /**
     * @return array
     */
    public function getInterceptorConfiguration() {
        return $this->interceptorConfiguration;
    }
}
?>
