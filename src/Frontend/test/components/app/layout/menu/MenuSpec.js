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

import React from 'react';
import Menu from '../../../../../components/app/layout/menu/Menu';
import { expect } from 'chai';
import { stub } from 'sinon';
import { shallow } from 'enzyme';
import MenuStore from '../../../../../store/MenuStore';

describe('Menu', () => {
  it('renders empty menu bar into document', () => {
    expect(shallow(<Menu items={[]} />).contains('MenuItem')).to.equal(false);
  });

  it('renders menu items', () => {
    stub(MenuStore, 'getState', () => ([
      {
        label: 'menu.start',
        url:   '/#/'
      }
    ]));

    const markup = shallow(<Menu items={[]} />, {
      context: {
        router: {
          isActive: () => false
        }
      }
    });

    markup.instance()._storeMenuItems();
    markup.update();

    const item = markup.find('MenuItem');
    expect(item.prop('url')).to.equal('/#/');
    expect(item.prop('label')).to.equal('menu.start');

    MenuStore.getState.restore();
  });
});
