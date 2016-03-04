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
    const roles = localStorage.getItem('user_roles');
    if (roles) {
      return -1 !== JSON.parse(roles).indexOf('ROLE_ADMIN');
    }

    return false;
  }

  /**
   * Getter for the api key.
   *
   * @returns {string} Api key of the current logged in user.
   */
  getApiKey() {
    return localStorage.getItem('api_key');
  }

  /**
   * Getter for the username.
   *
   * @returns {string} The username.
   */
  getUsername() {
    return localStorage.getItem('username');
  }

  /**
   * Stores the credentials as cookies.
   *
   * @param {Object} data Credentials.
   *
   * @returns {void}
   */
  addCredentials(data) {
    const {
      apiKey,
      roles,
      username
    } = data;

    localStorage.setItem('api_key', apiKey);
    localStorage.setItem('user_roles', JSON.stringify(roles.map(entity => entity.role)));
    localStorage.setItem('username', username);
  }
}

export default new ApiKeyService();
