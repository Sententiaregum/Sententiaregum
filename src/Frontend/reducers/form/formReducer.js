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

import {FORM_LOAD, FORM_CHANGE}    from '../../constants/Form';

const formReducer = (state = {}, action) => {

  if(action.type === FORM_LOAD) {
    return action.data
  }

  return state
};

export default formReducer;
