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
import Locale from '../../../util/http/LocaleService';
import handleLogin from '../../../store/handler/handleLogin';
import counterpart from 'counterpart';

describe('handleLogin', () => {
  it('manages locales after user event', () => {
    stub(Locale, 'getLocale', () => 'de');
    stub(counterpart, 'getLocale', () => 'en');
    stub(counterpart, 'setLocale');

    expect(handleLogin().locale).to.equal('de');
    expect(counterpart.setLocale.calledOnce).to.equal(true);
    expect(counterpart.setLocale.calledWith('de')).to.equal(true);

    Locale.getLocale.restore();
    counterpart.setLocale.restore();
    counterpart.getLocale.restore();
  });
});
