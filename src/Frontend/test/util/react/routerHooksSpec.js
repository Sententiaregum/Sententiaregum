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

import userStore from '../../../store/userStore';
import { protectPage, redirectToDashboard } from '../../../util/react/routerHooks';
import { expect } from 'chai';
import { stub, spy } from 'sinon';

describe('routerHooks', () => {
  it('protects page', () => {
    stub(userStore, 'getStateValue', () => false);
    const replace = spy();

    protectPage({}, replace);
    expect(replace.calledWith({ pathname: '/' })).to.equal(true);

    userStore.getStateValue.restore();
  });

  it('redirects to dashboard', () => {
    stub(userStore, 'getStateValue', () => true);
    const replace = spy();

    redirectToDashboard({}, replace);
    expect(replace.calledWith({ pathname: '/dashboard' })).to.equal(true);

    userStore.getStateValue.restore();
  });
});
