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

import MenuStore from '../../store/MenuStore';
import chai from 'chai';
import AppDispatcher from '../../dispatcher/AppDispatcher';
import Menu from '../../constants/Menu';
import sinon from 'sinon';

describe('MenuStore', () => {
  afterEach(() => MenuStore.items = []);

  describe('Implementation', () => {
    it('yields menu items', () => {
      let spy = sinon.spy();

      MenuStore.addChangeListener(spy, 'Menu');
      MenuStore.addItems([{url: '/#/test'}], {});

      chai.expect(MenuStore.getItems()).to.have.length(1);
      chai.assert(spy.called);
    });

    it('filters restricted user items', () => {
      let input = [
        {
          role: 'ROLE_ADMIN'
        },
        {
          logged_in: false
        },
        {
          logged_in: true
        },
        {}
      ];

      let authData = {
        is_admin:  false,
        logged_in: false
      };

      let result = MenuStore.getVisibleItems(input, authData);
      chai.expect(result).to.have.length(2);

      chai.expect(result.shift()).to.equal(input[1]);
      chai.expect(result.shift()).to.equal(input[3]);
    });
  });

  describe('Dispatcher Handling', () => {
    it('listens on dispatcher', () => {
      AppDispatcher.dispatch({
        event: Menu.TRANSFORM_ITEMS,
        items: [
          {
            url: '/#/',
            label: 'Home'
          }
        ],
        authData: {}
      });

      for (let val of MenuStore.getItems()) {
        chai.expect(val.label).to.equal('Home');
      }
    });
  });
});
