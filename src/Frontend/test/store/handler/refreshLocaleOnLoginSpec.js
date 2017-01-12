/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian@mbosch.me>
 * (c) Ben Bieler <ben@benbieler.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

import { stub } from 'sinon';
import { expect } from 'chai';
import handleLogin from '../../../store/handler/refreshLocaleOnLogin';
import counterpart from 'counterpart';

describe('refreshLocaleOnLogin', () => {
  it('manages locales after user event', () => {
    stub(counterpart, 'getLocale', () => 'en');
    stub(counterpart, 'setLocale');

    expect(handleLogin({ locale: 'de', success: true }, { locales: ['de', 'en'], current: { locale: 'en' } }))
      .to.deep.equal({ locales: ['de', 'en'], current: { locale: 'de' } });

    expect(counterpart.setLocale.calledOnce).to.equal(true);
    expect(counterpart.setLocale.calledWith('de')).to.equal(true);

    counterpart.setLocale.restore();
    counterpart.getLocale.restore();
  });

  it('returns previous state if locale is empty', () => {
    stub(counterpart, 'getLocale', () => 'en');

    expect(handleLogin({ success: false }, { locales: ['de', 'en'], current: { locale: 'en' } }))
      .to.deep.equal({ locales: ['de', 'en'], current: { locale: 'en' } });

    counterpart.getLocale.restore();
  });
});
