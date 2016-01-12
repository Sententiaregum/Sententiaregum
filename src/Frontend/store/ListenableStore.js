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
 * @abstract
 */
class ListenableStore extends Events.EventEmitter {
  /**
   * Adds a new change listener.
   *
   * @param {Function} callback The callback to trigger.
   * @param {string}   cls      The class name.
   *
   * @returns {void}
   */
  addChangeListener(callback, cls) {
    this.on(this.getEventNameByCls(cls), callback);
  }

  /**
   * Emits the store changes;
   *
   * @param {string} cls The class name.
   *
   * @returns {void}
   */
  emitChange(cls) {
    this.emit(this.getEventNameByCls(cls));
  }

  /**
   * Removes the change listener.
   *
   * @param {Function} callback The callback to trigger.
   * @param {string}   cls      The class name.
   *
   * @returns {void}
   */
  removeChangeListener(callback, cls) {
    this.removeListener(this.getEventNameByCls(cls), callback);
  }

  /**
   * Creates the event name.
   *
   * @param {string} cls The event class name.
   *
   * @returns {string} The generated event name.
   */
  getEventNameByCls(cls) {
    return `change_${cls}`;
  }
}

export default ListenableStore;
