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

describe('LanguageSwitcher', () => {
  it('renders the locales received from flux', () => {
    const locales = { 'de': 'Deutsch' };

    const store = {
      getState() {
        return {
          locales: {
            available: { 'de': 'Deutsch' },
            currentLocale: 'de'
          }
        };
      }
    };
    const markup = shallow(<LanguageSwitcher locales={locales} actions={{}} />, { context: { store } });

    expect(markup.find('LoadingDropDown')).to.have.length(0);

    const item = markup.find('DropDownItem');
    expect(item.prop('isActive')).to.equal(true);
    expect(item.prop('displayName')).to.equal('Deutsch');
  });
});
