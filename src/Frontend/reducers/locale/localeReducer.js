/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * (c) Ben Bieler <benjaminbieler2014@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import { CHANGE_LOCALE } from '../../constants/Locale';

const localeReducer = (state = [], action) => {
  switch (action.type) {
  case CHANGE_LOCALE:
    return state;

  default:
    return state;
  }
};

export default localeReducer;
