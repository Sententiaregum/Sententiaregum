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
import userStore from '../store/userStore';

/**
 * Action creator which is responsible for the menu related actions.
 *
 * @returns {Object} The action configuration.
 */
export default () => {
  /**
   * Action which triggers an analysation of menu items.
   *
   * Whenever something changes (e.g. the URL) the items need to be recomputed.
   * They're fetched from a configuration file and need to be processed and saved in the store, to keep everything available
   * in a certain instance (the stores) and ensure that always the right items are shown.
   *
   * @param {Function} publish Publisher which dispatches the items.
   * @param {Array}    items   The items to be processed.
   *
   * @returns {void}
   */
  function processMenuItems(publish, items) {
    publish({ items, authData: {
      logged_in: userStore.getStateValue('auth.authenticated'),
      is_admin:  userStore.getStateValue('auth.is_admin')
    } });
  }

  return { [TRANSFORM_ITEMS]: processMenuItems };
};
