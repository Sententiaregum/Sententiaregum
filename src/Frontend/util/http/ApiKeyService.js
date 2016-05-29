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
class ApiKeyService {
  /**
   * Checks if a user is logged in.
   *
   * @returns {boolean} Whether the user is logged in or not.
   */
  isLoggedIn() {
    return !!this.getApiKey();
  }

  /**
   * Checks if the current user is an admin.
   *
   * @returns {boolean} Whether the user is an admin or not.
   */
  isAdmin() {
    return false; // just for testing reasons, will be implemented in #35
  }

  /**
   * Getter for the api key.
   *
   * @returns {string} Api key of the current logged in user.
   */
  getApiKey() {
    return localStorage.getItem('api_key'); // todo refactor this in #35
  }
}

export default new ApiKeyService();
