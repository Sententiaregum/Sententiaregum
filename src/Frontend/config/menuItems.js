/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

/**
 * @author Benjamin Bieler <benjaminbieler2014@gmail.com>
 */

'use strict';

import MenuItem from '../menu/model/MenuItem';

function createItem(url, label, securityExpression = null) {
    return {
        url:                '/#!/' + url,
        label:              label,
        securityExpression: securityExpression
    }
}

let menuItems = [
    createItem('', 'Landing', 'Portal')
];

export default menuItems;
