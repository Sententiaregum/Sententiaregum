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

import MenuStore from '../../store/MenuStore';
import { expect } from 'chai';
import { runAction } from 'sententiaregum-flux-container';
import { TRANSFORM_ITEMS } from '../../constants/Menu';

describe('MenuStore', () => {
  it('handles menu changes', () => {
    runAction(() => {
      return dispatch => {
        dispatch(TRANSFORM_ITEMS, {
          items: [{
            url: '/#/'
          }],
          authData: {}
        });
      }
    }, []);

    expect(MenuStore.getState()[0].url).to.equal('/#/');
  });
});
