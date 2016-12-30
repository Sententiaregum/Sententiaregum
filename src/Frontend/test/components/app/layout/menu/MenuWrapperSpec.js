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

import MenuWrapper from '../../../../../components/app/layout/menu/MenuWrapper';
import Menu from '../../../../../components/app/layout/menu/Menu';
import React from 'react';
import { expect } from 'chai';
import { shallow } from 'enzyme';

describe('MenuWrapper', () => {
  it('renders menu component as child next to the brand', () => {
    const markup = shallow((
      <MenuWrapper actions={{}}>
        <Menu items={[{ url: '/#/login', label: 'Login' }]} actions={{}} />
      </MenuWrapper>
    ), {context: {router: {isActive: () => { return false}}}});

    const brand = markup.find('a');
    expect(brand.prop('href')).to.equal('/#/');
    expect(brand.contains('Sententiaregum')).to.equal(true);
    expect(markup.find(Menu).prop('items')).to.have.length(1);
  });

  it('renders no link into the brand if main page is active', () => {
    const markup = shallow((
      <MenuWrapper actions={{}}>
        <Menu items={[]} actions={{}} />
      </MenuWrapper>
    ), {context: {router: {isActive: () => true }}});

    const brand = markup.find('span');
    expect(brand.contains('Sententiaregum')).to.equal(true);
  });
});
