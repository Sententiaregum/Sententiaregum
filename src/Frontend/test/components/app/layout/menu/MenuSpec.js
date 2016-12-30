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

import React       from 'react';
import Menu        from '../../../../../components/app/layout/menu/Menu';
import { expect }  from 'chai';
import { stub }    from 'sinon';
import { shallow } from 'enzyme';
import MenuItem    from '../../../../../components/app/markup/MenuItem';

describe('Menu', () => {
  it('renders empty menu bar into document', () => {
    const wrapper = shallow(
      <Menu
        actions={{ buildMenuItems() {} }}
        items={[]} />
    );

    expect(wrapper.find(MenuItem)).to.have.length(0);
  });

  it('renders menu items', () => {
    const items = [
      {
        label: 'menu.start',
        url:   '/#/'
      }
    ];

    const markup = shallow(
      <Menu items={items} actions={{ buildMenuItems() {} }} />,
      { context: { router: { isActive: url => url === '/' } } }
    );

    const item = markup.find('MenuItem');
    expect(item.prop('url')).to.equal('/#/');
    expect(item.prop('label')).to.equal('menu.start');
    expect(item.prop('isActive')).to.equal(true);
  });
});
