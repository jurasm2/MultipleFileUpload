<?php

/**
 * This file is part of the MultipleFileUpload (https://github.com/jkuchar/MultipleFileUpload/)
 *
 * Copyright (c) 2013 Roman Vykuka (http://forum.nette.org/cs/profile.php?id=2221)
 * Copyright (c) 2013 Jan Kuchař (http://www.jankuchar.cz)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */


namespace MultipleFileUpload\UI\Swfupload;

use Nette\Environment;
use MultipleFileUpload\MultipleFileUpload;
use MultipleFileUpload\UI\AbstractInterface;

/**
 * Description of MFUUISwfupload
 *
 * @author Roman Vykuka, Jan Kuchař
 */
class Controller extends AbstractInterface {

	/**
	 * Getts interface base url
	 * @return type string
	 */
	function getBaseUrl() {
		return parent::getBaseUrl() . "swfupload";
	}
	
	/**
	 * Is this upload your upload? (upload from this interface)
	 */
	public function isThisYourUpload() {
		return (
			Environment::getHttpRequest()->getHeader('user-agent') === 'Shockwave Flash'
			AND isSet($_POST["sender"])
			AND $_POST["sender"] == "MFU-Swfupload"
		);
	}

	/**
	 * Handles uploaded files
	 * forwards it to model
	 */
	public function handleUploads() {
		if (!isset($_POST["token"])) {
			return;
		}

		/* @var $token string */
		$token = $_POST["token"];

		/* @var $file \Nette\Http\FileUpload */
		foreach (Environment::getHttpRequest()->getFiles() AS $file) {
			self::processFile($token, $file);
		}

		// Response to client
		echo "1";

		// End the script
		exit;
	}

	/**
	 * Renders interface to <div>
	 */
	public function render(MultipleFileUpload $upload) {
		$template = $this->createTemplate(dirname(__FILE__) . "/html.latte");
		$template->swfuId = $upload->getHtmlId() . "-swfuBox";
		return $template->__toString(TRUE);
	}

	/**
	 * Renders JavaScript body of function.
	 */
	public function renderInitJavaScript(MultipleFileUpload $upload) {
		$tpl = $this->createTemplate(dirname(__FILE__) . "/initJS.js");
		$tpl->sizeLimit = ini_get('upload_max_filesize') . 'B';
		$tpl->token = $upload->getToken();
		$tpl->maxFiles = $upload->maxFiles;
		$tpl->backLink = (string) $upload->form->action;
		$tpl->swfuId = $upload->getHtmlId() . "-swfuBox";
		$tpl->simUploadFiles = $upload->simUploadThreads;
		return $tpl->__toString(TRUE);
	}

	/**
	 * Renders JavaScript body of function.
	 */
	public function renderDestructJavaScript(MultipleFileUpload $upload) {
		return $this->createTemplate(dirname(__FILE__) . "/destructJS.js")->__toString(TRUE);
	}

	/**
	 * Renders set-up tags to <head> attribute
	 */
	public function renderHeadSection() {
		return $this->createTemplate(dirname(__FILE__) . "/head.latte")->__toString(TRUE);
	}

}