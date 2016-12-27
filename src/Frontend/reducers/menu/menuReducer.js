/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * (c) Ben Bieler <benjaminbieler2014@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
import { TRANSFORM_ITEMS }          from '../../constants/Menu';
import initialState                 from '../../config/initialState';
const menuReducer = (state = initialState, action) => {
  switch (action.type) {
  case TRANSFORM_ITEMS:
    return state;

  default:
    return state;
  }
};

export default menuReducer();
