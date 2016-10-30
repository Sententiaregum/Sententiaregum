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

/**
 * Creates a thennable object which evaluates postponed actions synchronously.
 *
 * @param {boolean} resolved Whether or not resolved.
 * @param {*}       payload  The resolved payload.
 *
 * @returns {Object} The further API.
 */
export default (resolved, payload) => () => ({
  /**
   * Thennable success handler.
   *
   * @param {Function} callback The success handler.
   *
   * @returns {then} Fluent interface of the API.
   */
  then(callback) {
    if (resolved) {
      callback(payload);
    }
    return this;
  },

  /**
   * Rejection handler.
   *
   * @param {Function} callback The error handler.
   *
   * @returns {catch} Fluent interface.
   */
  catch(callback) {
    let data = payload;
    if (typeof data === 'string') {
      data = new Error(data);
    }
    if (!resolved) {
      callback(data);
    }

    return this;
  }
});
