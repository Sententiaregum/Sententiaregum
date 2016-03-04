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

import axios from 'axios';
import ApiKey from '../http/ApiKeyService';

/**
 * API utils for account interactions.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class AccountWebAPIUtils {
  /**
   * Facade for creating accounts.
   *
   * @param {Object.<string>} formData Data to submit.
   * @param {Function} handler Response handler.
   * @param {Function} errorHandler Handler for validation errors.
   *
   * @returns {void}
   */
  createAccount(formData, handler, errorHandler) {
    axios.post('/api/users.json', formData)
      .then(response => handler(response.data))
      .catch(response => errorHandler(response.data));
  }

  /**
   * Activate user.
   *
   * @param {string} username Username to activate.
   * @param {string} key Activation key.
   * @param {Function} handler Handler for result processing.
   * @param {Function} errorHandler Error handler.
   *
   * @returns {void}
   */
  activate(username, key, handler, errorHandler) {
    axios.patch(`/api/users/activate.json?username=${username}&activation_key=${key}`, {})
      .then(() => handler())
      .catch(() => errorHandler());
  }

  /**
   * Requests an api key.
   *
   * @param {string} username Name of the user.
   * @param {string} password Password.
   * @param {Function} errorHandler Error handler.
   * @param {Function} handler Handler.
   *
   * @returns {void}
   */
  requestApiKey(username, password, errorHandler, handler) {
    axios.post('/api/api-key.json', { login: username, password })
      .then(response => {
        axios.get('/api/protected/users/credentials.json', { headers: { 'X-API-KEY': response.data.apiKey } } )
          .then(result => {
            ApiKey.addCredentials(result.data);
            handler(result.data);
          });
      })
      .catch(response => errorHandler(response.data));
  }
}

export default new AccountWebAPIUtils();
