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
import userStore from '../../store/userStore';
import menuActions from '../../actions/menuActions';
import TestUtils from 'sententiaregum-flux-container/lib/testing/TestUtils';
import { TRANSFORM_ITEMS } from '../../constants/Menu';

describe('menuActions', () => {

  it('publishes menu items in order to transform them', () => {
    stub(userStore, 'getStateValue', (path, defaultVal) => {
      if ('auth.authenticated' === path) {
        return false;
      }
      if ('auth.apiKey') {
        return false;
      }
      return defaultVal;
    });

    const items = [
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
    ];

    TestUtils.executeAction(menuActions, TRANSFORM_ITEMS, [items])({
      items,
      authData: {
        logged_in: false,
        is_admin:  false
      }
    });

    userStore.getStateValue.restore();
  });
});
