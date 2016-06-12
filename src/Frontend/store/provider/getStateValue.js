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
 * Store data fetcher.
 *
 * @param {Object} store        The store.
 * @param {String} value        The value to fetch.
 * @param {*}      defaultValue The default value.
 *
 * @returns {*} The state to fetch.
 */
export default (store, value, defaultValue = null) => {
  const state = store.getState();
  if ('undefined' === typeof state[value]) {
    return defaultValue;
  }

  return state[value];
};
