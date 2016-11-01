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

import { REQUEST_API_KEY, LOGOUT, CREATE_ACCOUNT, ACTIVATE_ACCOUNT } from '../constants/Portal';
import axios from 'axios';
import ApiKey from '../util/http/ApiKey';

/**
 * Action creator which is responsible for each user action.
 * The main actions are portal related as this area represents the main lifecycle of a user:
 *
 * - Login (request API key and authenticate with the API key)
 * - Logout (hand back the API key and go back to the portal)
 * - Activate (after a registration the account needs to be activated with a given key)
 * - Create (an account needs to be created)
 *
 * @returns {Object} A definition for all of the actions concerning users.
 */
export default () => {
  /**
   * Action implementation for the `login` action.
   *
   * @param {Function} publish  The callback which publishes the result.
   * @param {String}   username The username.
   * @param {String}   password The password.
   *
   * @returns {void}
   */
  function login(publish, { username, password }) {
    axios.post('/api/api-key.json', { login: username, password })
      .then(response => {
        axios.get('/api/protected/users/credentials.json', { headers: { 'X-API-KEY': response.data.apiKey } } )
          .then(result => {
            // add credentials to `localStorage`
            ApiKey.addCredentials(result.data);

            // publish fetched credentials
            const data = result.data;
            delete data['roles'];

            publish(Object.assign({}, data, {
              success:       true,
              authenticated: true,
              is_admin:      ApiKey.isAdmin()
            }));
          });
      })
      .catch(response => publish(Object.assign({}, response.data, { success: false, authenticated: false })));
  }

  /**
   * Action implementation for the `logout` action.
   *
   * @param {Function} publish The callback which publishes the result.
   *
   * @returns {void}
   */
  function logout(publish) {
    axios.delete('/api/api-key.json', { headers: { 'X-API-KEY': ApiKey.getApiKey() } })
      .then(() => {
        ApiKey.purgeCredentials();
        publish({ authenticated: false, success: false });
      });
  }

  /**
   * Action implementation for the `activate` action.
   *
   * @param {Function} publish  The callback which publishes the result.
   * @param {String}   username The username.
   * @param {String}   key      The key to activate.
   *
   * @returns {void}
   */
  function activate(publish, { username, key }) {
    axios.patch(`/api/users/activate.json?username=${username}&activation_key=${key}`, {})
      .then(() => publish({ success: true }))
      .catch(() => publish({ success: false }));
  }

  /**
   * Action implementation for the `create` action.
   *
   * @param {Function} publish  The callback which publishes the result.
   * @param {Object}   formData The form data for the account.
   *
   * @returns {void}
   */
  function createAccount(publish, formData) {
    axios.post('/api/users.json', formData)
      .then(response => publish(Object.assign({ success: true, name_suggestions: [], errors: {} }, response.data)))
      .catch(response => publish(Object.assign({ name_suggestions: [], success: false, id: null }, response.data)));
  }

  return {
    [REQUEST_API_KEY]:  login,
    [LOGOUT]:           logout,
    [ACTIVATE_ACCOUNT]: activate,
    [CREATE_ACCOUNT]:   createAccount
  };
};
