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

import Events from 'events';

/**
 * Simple configurable store which stores event callbacks.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class ListenableStore extends Events.EventEmitter {
  /**
   * Adds a new change listener.
   *
   * @param {Function} callback
   * @param {string} cls
   */
  addChangeListener(callback, cls) {
    this.on(this.getEventNameByCls(cls), callback);
  }

  /**
   * Emits the store changes;
   *
   * @param {string} cls
   */
  emitChange(cls) {
    this.emit(this.getEventNameByCls(cls));
  }

  /**
   * Removes the change listener.
   *
   * @param {Function} callback
   * @param {string} cls
   */
  removeChangeListener(callback, cls) {
    this.removeListener(this.getEventNameByCls(cls), callback);
  }

  /**
   * Creates the event name.
   *
   * @param {string} cls
   * @returns {string}
   */
  getEventNameByCls(cls) {
    return 'change_' + cls;
  }
}

export default ListenableStore;
