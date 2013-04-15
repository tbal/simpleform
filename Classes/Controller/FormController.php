<?php
namespace TYPO3\SimpleForm\Controller;

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
class FormController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

    /**
     * @var \TYPO3\SimpleForm\Utility\Form\FormDataHandler
     * @inject
     */
    private $fomDataHandler;

    /**
     * @var \TYPO3\SimpleForm\Utility\Form\StepHandler
     * @inject
     */
    private $stepHandler;

    /**
     * @var \TYPO3\SimpleForm\Utility\Validation\ValidationConfigurationHandler
     * @inject
     */
    private $validationConfigurationHandler;

    /**
     * @var \TYPO3\SimpleForm\Utility\Validation\ValidationErrorHandler
     * @inject
     */
    private $validationErrorHandler;

    /**
     * @var \TYPO3\SimpleForm\Utility\Session\SessionHandler
     * @inject
     */
    private $sessionHandler;

    /**
     * @var \TYPO3\SimpleForm\Utility\Session\SessionDataHandler
     * @inject
     */
    private $sessionDataHandler;

    /**
     * @var \TYPO3\SimpleForm\Utility\Validation\Validator
     * @inject
     */
    private $validator;

    /**
     * initialize
     */
    public function initializeAction() {
        $this->initializeFormDataHandler();
        $this->initializeStepHandler();
        $this->initializeValidationConfigurationHandler();
        $this->initializeSessionHandler();
    }

    /**
     * initialize formDataHandler
     */
    private function initializeFormDataHandler() {
        $this->fomDataHandler->setFormPrefix('form');
        $this->fomDataHandler->setGpData($this->request->getArguments());
    }

    /**
     * initialize validationConfigurationHandler
     */
    private function initializeValidationConfigurationHandler() {
        $this->validationConfigurationHandler->setTypoScriptSettings($this->settings);
    }

    /**
     * initialize SessionHandler
     */
    private function initializeSessionHandler() {
        $this->sessionHandler->setSessionDataStorageKey('simpleForm');
    }

    /**
     * initialize StepHandler
     */
    private function initializeStepHandler() {
        $this->stepHandler->setSteps($this->settings['steps']);
        $this->stepHandler->initialize();
    }

    /**
	 * action displayForm
	 *
	 * @return void
	 */
	public function displayFormAction() {
        if($this->fomDataHandler->formDataExists()) {
            $this->validate();
        } else {
            $this->stayOnCurrentStep();
        }
	}

    /**
     * validate formData of current step
     * TODO: refactor, add possibility to add validations to previous-step direction
     */
    private function validate() {
        if($this->stepHandler->getDirection() === \TYPO3\SimpleForm\Utility\Form\StepHandler::GO_TO_PREVIOUS_STEP) {
            $this->goToPreviousStep();
            $this->validator->setDeactivateCheck(true);
        } else {
            $this->validator->checkFormValues();
            if($this->validationErrorHandler->validationErrorsExists()) {
                $this->stayOnCurrentStepAfterFailedValidation();
            } else {
                $this->goToNextStep();
            }
        }
    }

    /**
     * stay on current Step
     */
    private function stayOnCurrentStep() {
        $this->view->assign('formData', $this->sessionDataHandler->getFormDataFromCurrentStep());
        $this->view->assign('step', $this->stepHandler->getCurrentStep());
    }

    /**
     * stay on current step after validation has failed
     */
    private function stayOnCurrentStepAfterFailedValidation() {
        $this->view->assign('step', $this->stepHandler->getCurrentStep());
        $this->view->assign('formData', $this->fomDataHandler->getFormDataFromCurrentStep());
        $this->view->assign('validationErrors', $this->validationErrorHandler->getValidationErrorsFromCurrentStep());
    }

    /**
     * go to next step
     */
    private function goToNextStep() {
        $this->sessionDataHandler->storeFormDataFromCurrentStep($this->fomDataHandler->getFormDataFromCurrentStep());
        $this->view->assign('formData', $this->sessionDataHandler->getFormDataFromStep($this->stepHandler->getNextStep()));
        $this->view->assign('step', $this->stepHandler->getNextStep());
    }

    /**
     * go to previous step
     */
    private function goToPreviousStep() {
        $this->sessionDataHandler->storeFormDataFromCurrentStep($this->fomDataHandler->getFormDataFromCurrentStep());
        $this->view->assign('formData', $this->sessionDataHandler->getFormDataFromStep($this->stepHandler->getPreviousStep()));
        $this->view->assign('step', $this->stepHandler->getPreviousStep());
    }
}
?>