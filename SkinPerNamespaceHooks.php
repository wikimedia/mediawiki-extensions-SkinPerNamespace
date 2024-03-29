<?php
/**
 * Hook function for RequestContextCreateSkin
 * This file is part of the MediaWiki extension SkinPerNamespace
 *
 * Copyright (C) 2012, Alexandre Emsenhuber
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

use MediaWiki\MediaWikiServices;

class SkinPerNamespaceHooks {
	/**
	 * Hook function for RequestContextCreateSkin
	 * @param IContextSource $context
	 * @param Skin &$skin
	 * @return bool
	 */
	public static function onSkinPerPageRequestContextCreateSkin( $context, &$skin ) {
		global $wgSkinPerNamespace, $wgSkinPerSpecialPage,
			$wgSkinPerNamespaceOverrideLoggedIn;

		if ( !$wgSkinPerNamespaceOverrideLoggedIn && $context->getUser()->isRegistered() ) {
			return true;
		}

		$title = $context->getTitle();
		if ( $title === null ) {
			return true;
		}
		$ns = $title->getNamespace();
		$skinName = null;

		if ( $ns === NS_SPECIAL ) {
			$specialPage = MediaWikiServices::getInstance()
				->getSpecialPageFactory()
				->resolveAlias( $title->getDBkey() );
			$canonical = $specialPage[0];

			if ( isset( $wgSkinPerSpecialPage[$canonical] ) ) {
				$skinName = $wgSkinPerSpecialPage[$canonical];
			}
		}

		if ( $skinName === null && isset( $wgSkinPerNamespace[$ns] ) ) {
			$skinName = $wgSkinPerNamespace[$ns];
		}

		if ( $skinName !== null ) {
			$skin = $skinName;
		}

		return true;
	}
}
