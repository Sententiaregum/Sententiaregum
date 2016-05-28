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

import { buildMenuItems } from '../../actions/MenuActions';
import { stub, assert, createStubInstance } from 'sinon';
import { expect } from 'chai';
import ApiKey from '../../util/http/ApiKeyService';
import Cookies from 'cookies-js';
import MenuStore from '../../store/MenuStore';
import { runAction } from 'sententiaregum-flux-container';

describe('MenuActions', () => {
  it('publishes menu items in order to transform them', () => {
    createStubInstance(Cookies);
    stub(ApiKey, 'isLoggedIn', () => false);

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

    ApiKey.isLoggedIn.restore();
    expect(MenuStore.getState().length).to.equal(1);
  });
});
