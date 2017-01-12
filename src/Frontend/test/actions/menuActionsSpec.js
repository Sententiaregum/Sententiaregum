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

import { spy }             from 'sinon';
import { expect }          from 'chai';
import { buildMenuItems }  from '../../actions/menuActions';
import { TRANSFORM_ITEMS } from '../../constants/Menu';

describe('menuActions', () => {
  it('publishes given menu items and related authentication info', () => {
    const items = [
      {
        label:  'Landing page',
        portal: true
      }
    ];

    const asyncAction = buildMenuItems(items);

    const asyncState = () => ({
      user: {
        security: {
          authenticated: false
        }
      }
    });

    const dispatch = spy();

    asyncAction(dispatch, asyncState);

    expect(dispatch.calledOnce).to.equal(true);
    expect(dispatch.calledWith({
      type:  TRANSFORM_ITEMS,
      items: [
        {
          label:  'Landing page',
          portal: true
        }
      ],
      authData: {
        logged_in: false,
        is_admin:  false
      }
    })).to.deep.equal(true);
  });
});
