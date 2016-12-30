/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * (c) Ben Bieler <benjaminbieler2014@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import userReducer    from '../../reducers/user/userReducer'
import menuReducer    from '../../reducers/menu/menuReducer'
import localeReducer  from '../../reducers/locale/localeReducer'

const reducers = {
  user:   userReducer,
  menu:   menuReducer,
  locales: localeReducer
};

export default reducers;
