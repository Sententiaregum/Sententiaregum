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
 * Redirect utility for hashbang redirects.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
export default class HashbangRedirect {
  /**
   * Constructor.
   *
   * @param {Object} window The window object.
   *
   * @returns {void}
   */
  constructor(window) {
    this.window = window;
  }

  /**
   * Redirects to another route
   *
   * @param {string} url The target url.
   *
   * @returns {void}
   */
  redirect(url) {
    this.window.location.href = `/#/${url}`;
  }
}
