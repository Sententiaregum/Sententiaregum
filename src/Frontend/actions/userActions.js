/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian@mbosch.me>
 * (c) Ben Bieler <ben@benbieler.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

import { CREATE_ACCOUNT, CREATE_FAIL }                             from '../constants/Portal';
import axios                                                       from 'axios';
import { SubmissionError }                                         from 'redux-form';

/**
 * Action implementation for the `create` action.
 *
 * @param {Object}   data The form data for the account.
 *
 * @returns {void}
 */
export const createAccount = data => dispatch =>
  axios.post('/api/users.json', data)
    .then(response => dispatch({
      type:    CREATE_ACCOUNT,
      payload: Object.assign({ success: true, name_suggestions: [] }, response.data)
    }))
    .catch(response => {
      dispatch({
        type:    CREATE_FAIL,
        payload: Object.assign({ success: false, name_suggestions: [] }, response.data)
      });

      return response.data.errors;
    })
    .then(e => Promise.reject(new SubmissionError(e)));
