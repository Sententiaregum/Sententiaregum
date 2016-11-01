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

import LanguageSwitcher from '../../../../components/app/widgets/LanguageSwitcher';
import { stub } from 'sinon';
import { expect } from 'chai';
import React from 'react';
import { shallow } from 'enzyme';
import { pure } from 'sententiaregum-flux-react';
import localeStore from '../../../../store/localeStore';
import Locale from '../../../../util/http/Locale';

describe('LanguageSwitcher', () => {
  it('renders the locales received from flux', () => {
    const locales = { 'de': 'Deutsch' };
    stub(localeStore, 'getStateValue', () => locales);
    stub(Locale, 'getLocale', () => 'de');

    const markup = shallow(pure(LanguageSwitcher, { locales }));

    expect(markup.find('LoadingDropDown')).to.have.length(0);

    const item = markup.find('DropDownItem');
    expect(item.prop('isActive')).to.equal(true);
    expect(item.prop('displayName')).to.equal('Deutsch');

    localeStore.getStateValue.restore();
    Locale.getLocale.restore();
  });

  it('shows loading bar', () => {
    const markup = shallow(pure(LanguageSwitcher));
    expect(markup.find('LoadingDropDown')).to.have.length(1);
  });
});
