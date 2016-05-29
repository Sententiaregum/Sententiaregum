/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * (c) Ben Bieler <benjaminbieler2014@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

import { TRANSFORM_ITEMS } from '../constants/Menu';
import ApiKey from '../util/http/ApiKeyService';

/**
 * Action creator to build the menu items.
 *
 * @param {Array} items The items.
 *
 * @returns {Function} The actual action.
 */
export function buildMenuItems(items) {
  return dispatch => {
    dispatch(TRANSFORM_ITEMS, {
      items,
      authData: {
        logged_in: ApiKey.isLoggedIn(),
        is_admin:  ApiKey.isAdmin()
      }
    });
  };
}
