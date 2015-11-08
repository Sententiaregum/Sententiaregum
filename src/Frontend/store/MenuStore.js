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

import ListenableStore from './ListenableStore';
import AppDispatcher from '../dispatcher/AppDispatcher';
import Menu from '../constants/Menu';

/**
 * Basic store which listens on menu handling events.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class MenuStore extends ListenableStore {
  /**
   * Adds new items
   *
   * @param {Array.<Object>} items
   * @param {Object.<boolean>} authData
   */
  addItems(items, authData) {
    if (!this.items) {
      this.items = [];
    }

    this.items = this.getVisibleItems(items, authData);
    this.emitChange('Menu');
  }

  /**
   * Returns all menu items.
   *
   * @returns {Array}
   */
  getItems() {
    return this.items;
  }

  /**
   * Gets a list of items being visible for the current user.
   *
   * @param {Array.<Object>} items
   * @param {Object.<boolean>} authData
   *
   * @returns {Array.<Object>}
   */
  getVisibleItems(items, authData) {
    return items.filter((item) => !(item.role === 'ROLE_ADMIN' && !authData.is_admin || item.logged_in && !authData.logged_in));
  }
}

const store = new MenuStore();

AppDispatcher.register((payload) => {
  if (payload.event === Menu.TRANSFORM_ITEMS) {
    store.addItems(payload.items, payload.authData);
  }
});

export default store;
