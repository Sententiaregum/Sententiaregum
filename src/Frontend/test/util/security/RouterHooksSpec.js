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

import UserStore from '../../../store/UserStore';
import { protectPage, redirectToDashboard } from '../../../util/security/RouterHooks';
import { expect } from 'chai';
import { stub, spy } from 'sinon';

describe('RouterHooks', () => {
  it('protects page', () => {
    stub(UserStore, 'getState', () => ({ is_logged_in: false }));
    const replace = spy();

    protectPage({}, replace);
    expect(replace.calledWith({ pathname: '/' })).to.equal(true);

    UserStore.getState.restore();
  });

  it('redirects to dashboard', () => {
    stub(UserStore, 'getState', () => ({ is_logged_in: true }));
    const replace = spy();

    redirectToDashboard({}, replace);
    expect(replace.calledWith({ pathname: '/dashboard' })).to.equal(true);

    UserStore.getState.restore();
  });
});
