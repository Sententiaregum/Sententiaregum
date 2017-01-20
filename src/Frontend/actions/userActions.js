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

import { REQUEST_API_KEY,
         LOGOUT,
         CREATE_ACCOUNT,
         CREATE_FAIL,
         ACTIVATE_ACCOUNT }                                        from '../constants/Portal';
import axios                                                       from 'axios';


/**
 * Action implementation for the `create` action.
 *
 * @param {Object}   data The form data for the account.
 *
 * @returns {void}
 */
export const createAccount = data => (dispatch) => {
  axios.post('/api/users.json', data)
    .then(response => dispatch({
      type:    CREATE_ACCOUNT,
      payload: Object.assign({ success: true, name_suggestions: [], errors: {} }, response.data)
    }))
    .catch(response => dispatch({
      type:    CREATE_FAIL,
      payload: Object.assign({ name_suggestions: [], success: false, id: null }, response.data)
    }));
};


