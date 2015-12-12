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
 * Service which is responsible for handling the interaction with api keys.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
export default class ApiKeyService {
  /**
   * Constructor.
   *
   * @param {Object} cookieFactory
   */
  constructor(cookieFactory) {
    this.cookieFactory = cookieFactory;
  }

  /**
   * Checks if a user is logged in.
   *
   * @returns {boolean}
   */
  isLoggedIn() {
    return !!this.getApiKey();
  }

  /**
   * Checks if the current user is an admin.
   *
   * @returns {boolean}
   */
  isAdmin() {
    return false; // just for testing reasons, will be implemented in #35
  }

  /**
   * Getter for the api key.
   *
   * @returns {string}
   */
  getApiKey() {
    return this.cookieFactory.getCookies().get('api_key');
  }
}
