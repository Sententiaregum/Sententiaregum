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

import { stub } from 'sinon';
import { expect } from 'chai';
import MenuStore from '../../store/MenuStore';
import UserStore from '../../store/UserStore';
import { runAction } from 'sententiaregum-flux-container';
import { buildMenuItems } from '../../actions/MenuActions';

describe('MenuActions', () => {

  it('publishes menu items in order to transform them', () => {
    stub(UserStore, 'getState', () => ({ is_logged_in: false }));
    runAction(buildMenuItems, [[
      {
        url: '/#/',
        label: 'Start'
      },
      {
        url: '/#/admin',
        label: 'Admin',
        role: 'ROLE_ADMIN',
        logged_in: true
      }
    ]]);

    expect(MenuStore.getState().length).to.equal(1);
    UserStore.getState.restore();
  });
});
