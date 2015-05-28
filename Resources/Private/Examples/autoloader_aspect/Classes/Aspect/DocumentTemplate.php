<?php
/**
 * This Example test the aspects before & after.
 *
 * @category Extension
 * @package  AutoloaderAspect\Aspect
 * @author   Carsten Biebricher
 */

namespace HDNET\AutoloaderAspect\Aspect;

/**
 * This Example test the aspects before & after.
 *
 * @author Carsten Biebricher
 */
class DocumentTemplate {

	/**
	 * Change the method-parameter BEFORE the joinPoint (original method) is called.
	 *
	 * @param object $object class of the joinPoint
	 * @param array  $params arguments of the joinPoint
	 *
	 * @return array
	 *
	 * @aspectClass \TYPO3\CMS\Backend\Template\DocumentTemplate
	 * @aspectJoinPoint header
	 * @aspectAdvice    before
	 */
	public function headerBefore($object, $params) {
		$params['args'][0] .= ' ASPECT (before)';
		return $params;
	}

	/**
	 * Change the method-parameter BEFORE the joinPoint (original method) is called.
	 *
	 * @param object $object class of the joinPoint
	 * @param array  $params arguments of the joinPoint
	 *
	 * @return array
	 *
	 * @aspectClass \TYPO3\CMS\Backend\Template\DocumentTemplate
	 * @aspectJoinPoint header
	 * @aspectAdvice    before
	 */
	public function headerBefore2($object, $params) {
		$params['args'][0] .= ' -2-';
		return $params;
	}

	/**
	 * Change the method-result After the joinPoint (original method) is called.
	 *
	 * @param object $object class of the joinPoint
	 * @param array  $params arguments of the joinPoint
	 *
	 * @return array
	 *
	 * @aspectClass \TYPO3\CMS\Backend\Template\DocumentTemplate
	 * @aspectJoinPoint header
	 * @aspectAdvice    after
	 */
	public function headerAfter($object, $params) {
		$params['result'] .= ' <h2>ASPECT (after)</h2>';
		return $params;
	}
}