/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * (c) Ben Bieler <benjaminbieler2014@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import { GET_LOCALES, CHANGE_LOCALE }   from '../../constants/Locale';
import Locale                           from '../../util/http/Locale';

const localeReducer = (state = [], action) => {
  switch (action.type) {
  case GET_LOCALES:
    return state;

  case CHANGE_LOCALE:
    Locale.setLocale(action.locale);
    return state;

  default:
    return state;
  }
};

export default localeReducer;
