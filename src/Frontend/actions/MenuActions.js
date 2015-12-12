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

import AppDispatcher from '../dispatcher/AppDispatcher';
import Menu from '../constants/Menu';
import {ApiKey} from '../util/http/facade/HttpServices';

/**
 * Action creator that executes menu lifecycle actions such as menu creation.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class MenuActions {
  /**
   * Publishes all pre-configured menu items to a store.
   *
   * @param {Array.<string>} items
   */
  buildMenuItems(items) {
    AppDispatcher.dispatch({
      event:    Menu.TRANSFORM_ITEMS,
      items:    items,
      authData: {
        logged_in: ApiKey.isLoggedIn(),
        is_admin:  ApiKey.isAdmin()
      }
    });
  }
}

export default new MenuActions;
